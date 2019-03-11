<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
    <xsl:output method="xml" encoding="utf-8" indent="no"/>

    <xsl:template match="root">
        <items>
            <xsl:apply-templates />
        </items>
    </xsl:template>

    <xsl:template match="data">
        <item>

            <order_id>
                <xsl:value-of select="order_id"/>
            </order_id>

            <click_id>
                <xsl:value-of select="click_id"/>
            </click_id>

            <xsl:variable name="orderamount_new">
                <xsl:value-of select="translate(orderamount, ',' , '')"/>
            </xsl:variable>

            <order_total>
                <xsl:value-of select="substring($orderamount_new,2)"/>
            </order_total>

            <currency>CNY</currency>

            <customer_type>
                <xsl:choose>
                    <xsl:when test="ordertype = 'HotelInternate'">H</xsl:when>
                    <xsl:when test="ordertype = 'HotelDomestic'">H</xsl:when>
                    <xsl:when test="ordertype = 'FlightInternate'">F</xsl:when>
                    <xsl:when test="ordertype = 'FlightDomestic'">F</xsl:when>
                    <xsl:otherwise></xsl:otherwise>
                </xsl:choose>
            </customer_type>

        </item>
    </xsl:template>
</xsl:stylesheet>