<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  version="1.0">

        <xsl:output method='html' />

        <xsl:template match='text()' />

        <xsl:template match="students">
                <div class="header">
                        <select id="sort">
                                <option value="default">Default</option>
                                <option value="gender">Gender</option>
                                <option value="age">Age</option>
                                <option value="language">Language</option>
                        </select>
                        <div>
                                <div class="center">
                                        Statements results
                                </div>
                                <div id='logo'>
                                        <img src="../images/schoollogo.png" height="50px"/>
                                </div>
                        </div>
                </div>

                <div class="table" id="t">
                        <table id="results">
                                <thead id='headers'>
                                        <tr>
                                                <th class="details">Name</th>
                                                <th class="details">Form Group</th>
                                                <th class="details">Gender</th>
                                                <th class="details">Language</th>
                                                <th class="details">Date of Birth</th>
                                                <th class="details">Nationality</th>
                                                <xsl:call-template name="assessment">
                                                        <xsl:with-param name="assessments" select="student[1]/assessments/assessment"/>
                                                        <xsl:with-param name="scores" select="0" />
                                                        <xsl:with-param name="totals" select="0" />
                                                </xsl:call-template>
                                                <xsl:call-template name="assessment">
                                                        <xsl:with-param name="assessments" select="student[1]/assessments/assessment"/>
                                                        <xsl:with-param name="scores" select="0" />
                                                        <xsl:with-param name="totals" select="1" />
                                                </xsl:call-template>
                                        </tr>
                                </thead>
                                <tbody id='students'>
                                        <xsl:apply-templates select="student"/>
                                </tbody>
                                <tfoot id='averages'>
                                        <tr>
                                                <th colspan="6" class="averages">Average</th>
                                        </tr>
                                </tfoot>
                        </table>
                        <div id="chart-data"></div>
                </div>
        </xsl:template>

        <xsl:template match="student">
                <tr>
                        <td class="details name"><xsl:value-of select="displayfullname/value/text()" /></td>
                        <td class="details"><xsl:value-of select="registrationgroup/value/text()" /></td>
                        <td class="details"><xsl:value-of select="gender/value/text()" /></td>
                        <td class="details"><xsl:value-of select="language/value/text()" /></td>
                        <td class="details"><xsl:value-of select="dob/value/text()" /></td>
                        <td class="details"><xsl:value-of select="nationality/value/text()" /></td>
                        <xsl:call-template name="assessment">
                                <xsl:with-param name="assessments" select="assessments/assessment"/>
                                <xsl:with-param name="scores" select="1" />
                                <xsl:with-param name="totals" select="0" />
                        </xsl:call-template>
                        <xsl:call-template name="assessment">
                                <xsl:with-param name="assessments" select="assessments/assessment"/>
                                <xsl:with-param name="scores" select="1" />
                                <xsl:with-param name="totals" select="1" />
                        </xsl:call-template>
                </tr>
        </xsl:template>

	<xsl:template match="student">
                <tr>
                        <td class="details name"><xsl:value-of select="displayfullname/value/text()" /></td>
                        <td class="details"><xsl:value-of select="registrationgroup/value/text()" /></td>
                        <td class="details"><xsl:value-of select="gender/value/text()" /></td>
                        <td class="details"><xsl:value-of select="language/value/text()" /></td>
                        <td class="details"><xsl:value-of select="dob/value/text()" /></td>
                        <td class="details"><xsl:value-of select="nationality/value/text()" /></td>
                        <xsl:call-template name="assessment">
                                <xsl:with-param name="assessments" select="assessments/assessment"/>
                                <xsl:with-param name="scores" select="1" />
                                <xsl:with-param name="totals" select="0" />
                        </xsl:call-template>
                        <xsl:call-template name="assessment">
                                <xsl:with-param name="assessments" select="assessments/assessment"/>
                                <xsl:with-param name="scores" select="1" />
                                <xsl:with-param name="totals" select="1" />
                        </xsl:call-template>
                </tr>
        </xsl:template>

        <xsl:template name="assessment">
                <xsl:param name="scores"></xsl:param>
                <xsl:param name="totals"></xsl:param>
                <xsl:param name="assessments"></xsl:param>
                <xsl:for-each select="$assessments">
                        <xsl:choose>
                                <xsl:when test="$totals=0 and component/text()!=''">
                                        <xsl:choose>
                                                <xsl:when test="$scores=1">
                                                        <td>
                                                                <xsl:value-of select="score/text()" />
                                                        </td>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                        <th>
                                                                <xsl:value-of select="name/text()" />&#160;<xsl:value-of select="component/text()" />
                                                        </th>
                                                </xsl:otherwise>
                                        </xsl:choose>
                                </xsl:when>
                                <xsl:when test="$totals=1 and (component/text()='' or not(component/text())) and name/text()!=''">
                                        <xsl:choose>
                                                <xsl:when test="$scores=1">
                                                        <td class="totals">
                                                                <xsl:value-of select="score/text()" />
                                                        </td>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                        <th class="totals">
                                                                <xsl:value-of select="name/text()" />
                                                        </th>
                                                </xsl:otherwise>
                                        </xsl:choose>
                                </xsl:when>
                                <xsl:otherwise>
                                </xsl:otherwise>
                         </xsl:choose>
                </xsl:for-each>
        </xsl:template>

</xsl:stylesheet>

